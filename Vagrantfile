# -*- mode: ruby -*-
# vi: set ft=ruby :

# This is a Vagrant configuration file. See http://vagrantup.com/ for more.

# Quick howto:
# $ cp www/secret/db_user.php.sample www/secret/db_user.php
# $ gem install vagrant
# $ vagrant box add lucid32 http://files.vagrantup.com/lucid32.box
# $ vagrant up
# ... work work work ...
# $ vagrant halt

# To access the VM, run `vagrant ssh`.
# The VM is bound to IP address `192.168.33.17` so the website should be
# accessible at http://192.168.33.17/.
# In the VM, the source code repository is mounted at `/vagrant` as a shared
# folder.


Vagrant::Config.run do |config|
  config.vm.box = "lucid32"
  config.vm.network :hostonly, "192.168.33.17"
  config.vm.provision :shell, :path => "vagrant-provision.sh"
end
