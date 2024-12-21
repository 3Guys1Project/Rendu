#!/bin/bash

if [ "$1" = "prod" ]; then
    echo "Build de l'image Docker en mode production..."
    cp .env.prod ./app/.env
    cp .env.prod ./app/.env.dev
    docker compose -f compose.yaml -f compose.prod.yaml up -d --build
else
    echo "Build de l'image Docker en mode développement..."
    cp .env.dev ./app/.env
    cp .env.dev ./app/.env.dev
    docker compose -f compose.yaml up -d --build
fi
