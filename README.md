# ecommerce-symfony-project
Project to be carried out in Symfony and Ansible as part of my training at Epitech in 2023

Installation d'Ansible : sudo apt-get -y install ansible

Création de clé privé/public en local puis envoie de la clé publique au serveur autoriser : ssh-keygen ssh-copy-id -i ~/.ssh/id_rsa.pub root@192.168.0.100

Lancer le script: ansible-playbook playbook.yml
