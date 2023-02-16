#!/bin/sh

# docker exec -it -w '/var/www/html/' ddev-pmd-web bash -c 'phpcs'

# Via https://github.com/ikappas/vscode-phpcs/issues/173#issuecomment-1289000156
# - not yet working for phpcs (deprecated) nor PHP Sniffer & Beautifier.
# Posted request for
# https://marketplace.visualstudio.com/items?itemName=ValeryanM.vscode-phpsab
# extension here: https://github.com/valeryan/vscode-phpsab/issues/88
LOCAL_PATH='/Users/michael/sites/pmd-2023-winter/pmd/'
REMOTE_PATH='/app/'

for param; do
  if [[ "$param" == *"$LOCAL_PATH"* ]]; then
    param=${param//$LOCAL_PATH/$REMOTE_PATH}
  fi

  newparams+=("$param")
done

/usr/local/bin/docker-compose exec -T php /app/vendor/bin/phpcs ${newparams[@]}
