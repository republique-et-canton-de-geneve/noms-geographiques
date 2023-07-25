#    .--.
#   |o_o |
#   |:_/ |
#  //   \ \
# (|     | )
#/'\_   _/`\
#\___)=(___/
#+-------------------------------------------+
#| Node avec setting des proxys de la maison |
#| et du certificat self-signed.             |
#+-------------------------------------------+
FROM wodby/node:16-dev-1.2.0
# Dans le cadre d'une migration d'un projet de NPM vers YARN
# 1- décommentez cette ligne pour lancer la génération automatique du fichier yarn.lock
# 2- Vérifiez que  la procédure ne lève aucune erreur et que le fichier yarn.lock est bien créé
# 3- Supprimez le fichier package-lock.json
#    yarn import

# Installation de Gulp avec Yarn.
RUN yarn cache clean --all
RUN yarn global add gulp-cli
RUN yarn install

