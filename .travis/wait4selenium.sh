#!/bin/sh

# Loop until selenium servier is available
printf 'Waiting Seleinum Server to load'
until $(curl --output /dev/null --silent --head --fail http://localhost:4444/wd/hub); do
    printf '.'
    sleep 1
done