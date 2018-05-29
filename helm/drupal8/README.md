Build the PHP and Nginx Docker images:
```
docker build -t gcr.io/personal-dev-environment/php -t gcr.io/personal-dev-environment/php:latest drupal8
docker build -t gcr.io/personal-dev-environment/nginx -t gcr.io/personal-dev-environment/nginx:latest -f drupal8/Dockerfile.nginx drupal8
docker build -t gcr.io/personal-dev-environment/varnish -t gcr.io/personal-dev-environment/varnish:latest -f drupal8/Dockerfile.varnish drupal8
```
Push your images to your Docker registry, example with Google Container Registry:
```
gcloud docker -- push gcr.io/personal-dev-environment/php
gcloud docker -- push gcr.io/personal-dev-environment/nginx
gcloud docker -- push gcr.io/personal-dev-environment/varnish
```

Deploy your API to the container:

```
helm install ./drupal8/helm/drupal8 --namespace=drupal8 --name sf \
    --set php.repository=gcr.io/personal-dev-environment/php \
    --set nginx.repository=gcr.io/personal-dev-environment/nginx \
    --set secret=MyAppSecretKey \
    --set mysql.mysqlDatabase=MyPgPassword \
    --set mysql.mysqlRootPassword=root \
    --set mysql.mysqlUser=dev \
    --set mysql.mysqlPassword=dev \
    --set corsAllowUrl='^https?://[a-z\]*\.mywebsite.com$'
```
