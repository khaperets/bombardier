#!/bin/bash

rm -rf ./targets
mkdir -p targets
php targets.php --replicas=100
kubectl delete --all deployments
kubectl apply -f targets
