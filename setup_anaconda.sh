# written by Duy-Dinh Le

# install anaconda on ubuntu

# Copy from local to remote host
# scp ./Documents/GitHub/tdsjp/setup_anaconda.sh mmlab@192.168.2x.6x: 

# Go to home directory
cd ~

# You can change what anaconda version you want at 
# https://repo.continuum.io/archive/

wget https://repo.continuum.io/archive/Anaconda3-5.1.0-Linux-x86_64.sh

# run installation
bash Anaconda3-5.1.0-Linux-x86_64.sh -b -p ~/anaconda

rm Anaconda3-5.1.0-Linux-x86_64.sh

# update .bashrc
echo 'export PATH="~/anaconda/bin:$PATH"' >> ~/.bashrc 

# view update
cat ~/.bashrc 

# Refresh basically
source .bashrc

conda update conda

# install nb_conda
conda install nb_conda 