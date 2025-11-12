Code réalisé une fois le ct créée sur proxmox 

'''bash
apt-get install upgrade
'''

----

'''bash
apt-get install sudo
'''

Maintenant installons Docker : 
'''bash
# Add Docker's official GPG key:
sudo apt update
sudo apt install ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Add the repository to Apt sources:
sudo tee /etc/apt/sources.list.d/docker.sources <<EOF
Types: deb
URIs: https://download.docker.com/linux/debian
Suites: $(. /etc/os-release && echo "$VERSION_CODENAME")
Components: stable
Signed-By: /etc/apt/keyrings/docker.asc
EOF

sudo apt update
'''

puis 

'''bash
sudo apt install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
'''

# Vérifions que Docker est bien installé
'''bash
sudo docker --version
'''

Maintenant nous allons faire deux dockers : 
1 pour mysql 
1 pour mon site 