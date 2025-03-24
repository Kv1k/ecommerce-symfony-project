Installation d'Ansible :
sudo apt-get -y install ansible

Création de clé privé/public en local puis envoie de la clé publique au serveur autoriser :
ssh-keygen
ssh-copy-id -i ~/.ssh/id_rsa.pub root@192.168.0.100

Lancer le script:
ansible-playbook playbook.yml