#!/usr/bin/env bash

site_workspace=$1
site_uri=$2
php_path=$3
site_alias=$4
git_branch=$5
db_path=$6

drush_cmd="${php_path}/php -d memory_limit=2048M ${site_workspace}/branches/${git_branch}/vendor/drush/drush/drush"

export PATH=${php_path}:$PATH

#########
# Partie 1 : conf env

## version locale
#cd ${site_workspace}

#Initialise une DB ge8 vide + applique toutes les configs : voir ce fichier pour le détail
#./docker/drupal-init.sh

#cd ${site_workspace}/htdocs

#pour faire la migration complète / START => 3 lignes suivantes pas utiles si on utilise docker/drupal-init.sh
# Activation des modules migrate
##drush en -y migrate migrate_upgrade migrate_plus migrate_tools migrate_drupal migrate_drupal_multilingual
##drush en  edg_migrate

#########
## version serveur
# Préparer le dossier site_pilote avec les fichiers à jour par rapport à la db d7
# A lancer sur le serveur si pas de partage de clef ssh:
# rsync -rzh  drupalinternet@www-dev-2:/srv/www/9950_drupalinternet/media/site_pilote /srv/www/0001_site/media
rsync -rzh intcti@www-dev-2:/srv/www/9950_drupalinternet/media/site_pilote /srv/www/0001_site/media

sudo /bin/chown -R intcti ${site_workspace}/media
chown -R intcti:site0001 ${site_workspace}/media
chmod g+w -R ${site_workspace}/media

# Supprimer le dossier media/d7 avant import
cd ${site_workspace}/media
rm -rf d7/*

# C'est parti
cd ${site_workspace}/htdocs
chmod +x ${site_workspace}/branches/${git_branch}/vendor/drush/drush/drush

# init db vierge
${drush_cmd} sql-drop -y
${drush_cmd} sql-cli < ${db_path}/gech_init-migration-4.sql

# Mode maintenance pour la migration
${drush_cmd} sset system.maintenance_mode 1

chmod +x ${site_workspace}/branches/${git_branch}/scripts/integration_continue/update.sh
. ${site_workspace}/branches/${git_branch}/scripts/integration_continue/update.sh ${site_workspace} ${php_path} ${git_branch}

################
# Partie 2 : migration
# Ordre des migrations (sans les revisions):

# Active les modules de migration
${drush_cmd} en -y migrate migrate_upgrade migrate_plus migrate_tools migrate_drupal migrate_drupal_multilingual
${drush_cmd} en -y edg_migrate

# Importe les fichiers de migration edg_migrate
${drush_cmd} config-import --partial --source=modules/custom/edg_migrate/config/install -y

# la commande ci-dessous lance toutes les migration gech_drupal_7 mais pour le moment l'ordre est à controler
##drush migrate:import --group=gech_drupal_7


${drush_cmd} mim upgrade_d7_user

# avant les deux cmd suivantes, s'assurer du chemin "source_base_path" dans les fichiers de migration
# local     "source_base_path: /var/www/html"
# serveur   "source_base_path: /srv/www/0001_site"
${drush_cmd} mim upgrade_d7_file_images
${drush_cmd} mim upgrade_d7_file_documents

${drush_cmd} mim upgrade_d7_taxonomy_term_autres_auteurs
${drush_cmd} mim upgrade_d7_taxonomy_term_blogs
${drush_cmd} mim upgrade_d7_taxonomy_term_membres_conseil_etat
${drush_cmd} mim upgrade_d7_taxonomy_term_organisations
${drush_cmd} mim upgrade_d7_taxonomy_term_publications
${drush_cmd} mr upgrade_d7_taxonomy_term_publications
${drush_cmd} mim upgrade_d7_taxonomy_term_publications
${drush_cmd} mim upgrade_d7_taxonomy_term_themes
${drush_cmd} mim upgrade_d7_taxonomy_term_rubrique_point_presse
${drush_cmd} mim upgrade_d7_taxonomy_term_prestations
${drush_cmd} mim upgrade_d7_taxonomy_term_type_promo
${drush_cmd} mim upgrade_d7_node_article_de_blog
${drush_cmd} mim upgrade_d7_node_prestation
${drush_cmd} mim upgrade_d7_node_actualite
${drush_cmd} mim upgrade_d7_node_document
${drush_cmd} mim upgrade_d7_node_synthese
${drush_cmd} mim upgrade_d7_node_dossier
${drush_cmd} mim upgrade_d7_node_page
${drush_cmd} mim upgrade_d7_node_communique_presse
${drush_cmd} mim upgrade_d7_node_point_presse
${drush_cmd} mim upgrade_d7_node_extrait_point_presse
${drush_cmd} mim upgrade_d7_taxonomy_term_livrets
${drush_cmd} mim upgrade_d7_node_evenement

# Revert + import de la migration a_votre_service pour regénération des liens vers les entrées feuillets
${drush_cmd} mr upgrade_d7_taxonomy_term_prestations
${drush_cmd} mim upgrade_d7_taxonomy_term_prestations

${drush_cmd} mim upgrade_d7_paragraph_timeline_items
${drush_cmd} mim --tag=paragraph
${drush_cmd} mim upgrade_d7_node_page_promo
${drush_cmd} mim upgrade_d7_node_translation_page_promo

#sauvegarde de la db si toutes les migrations sont OK permet de revenir à cet état de DB si les commandes drush suivantes plantes
${drush_cmd} sql-dump > ./`date +"%Y_%m_%d"`_migration-partial.dump

#faire ensuite les commandes drush de migration
${drush_cmd} edg_migrate:renameWrongFilenames
${drush_cmd} edg_migrate:livretsTraduction fr
${drush_cmd} edg_migrate:feuilletsUpdate
${drush_cmd} edg_migrate:extraitsUpdate
${drush_cmd} edg_migrate:parent_taxo dossiers 6 migrate_map_upgrade_d7_taxonomy_term_themes
${drush_cmd} edg_migrate:sousThemeTransfer
${drush_cmd} edg_migrate:tokens
${drush_cmd} edg_migrate:textReplacement
${drush_cmd} edg_migrate:eventAllDaySet
${drush_cmd} edg_migrate:paragraphs
${drush_cmd} edg_migrate:parent_taxo a_votre_service 3 migrate_map_upgrade_d7_taxonomy_term_prestations
${drush_cmd} edg_migrate:parent_taxo type_publication 8 migrate_map_upgrade_d7_taxonomy_term_publications
${drush_cmd} edg_migrate:parent_taxo organisations 7 migrate_map_upgrade_d7_taxonomy_term_organisations

${drush_cmd} sql-dump --tables-list=annuaireofficiel --database="drupal7" > exportannuaire.sql
${drush_cmd} sql-cli < exportannuaire.sql
${drush_cmd} gech_annuaire:sync

# ${drush_cmd} config-import --partial --source=modules/custom/edg_migrate/config/webforms -y

# supprime tous les alias générés lors de la migration
${drush_cmd} pathauto:aliases-delete all

${drush_cmd} mim upgrade_d7_shurly
${drush_cmd} mim upgrade_d7_alias_lock
# ${drush_cmd} mim upgrade_d7_alias_redirect

# Génère les alias URL pour tous les types de contenu ayant un pattern défini
${drush_cmd} pathauto:aliases-generate all all

# nettoyage de l'alias parcourir qui pose problème avec la route D8
${drush_cmd} sqlq "delete from redirect where redirect_redirect__uri LIKE '%parcourir%'"

# Ajout de Redirect depuis /dossier vers /theme
#${drush_cmd} edg_migrate:addRedirect "dossier" "/theme"

# Vérification et importation des traductions
${drush_cmd} locale-check
${drush_cmd} locale-update

# Applique les updates une fois les data importées et transférées dans la nouvelle structure
${drush_cmd} cr
${drush_cmd} updb -y

${drush_cmd} pmu gech_offres_emploi
${drush_cmd} en gech_offres_emploi -y

#reset les dates de dernière modification
${drush_cmd} edg_migrate:resetChangedTime

#nettoyage des redirects ********
${drush_cmd} sqlq  "delete from redirect where redirect_source__path like '%taxonomy%';"
${drush_cmd} sqlq  "delete from redirect where redirect_source__path like '%node%';"

sudo /bin/chown -R intcti ${site_workspace}/media
chown -R intcti:site0001 ${site_workspace}/media
chmod g+w -R ${site_workspace}/media

# fin mode maintenance
${drush_cmd} sset system.maintenance_mode 0

# // Rebuild the sitemap queue for all sitemap variants.
${drush_cmd} ssr

# export de la db migrée
${drush_cmd} sql-dump > ./`date +"%Y_%m_%d"`_post_migration-except_revisions.dump

## Prier ##


############
# commandes utiles
#drush uublk admin
#drush upwd admin smil

#drush migrate:rollback --group=gech_drupal_7

#drush migrate:reset:status upgrade_d7_taxonomy_term_themes
#drush mrs upgrade_d7_taxonomy_term_themes
#drush migrate:rollback --tag=Taxonomy
#drush mr --tag=Taxonomy
