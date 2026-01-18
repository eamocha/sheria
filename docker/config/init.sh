#!/usr/bin/env bash

mysql -u "$APP4LEGAL_DB_USER" -p "$APP4LEGAL_DB_PASSWORD" -e "CREATE DATABASE app4legal"

mysql -u "$APP4LEGAL_DB_USER" -p "$APP4LEGAL_DB_PASSWORD" app4legal < /var/www/html/assets/db/next-release/MYSQL/1-install.sql
