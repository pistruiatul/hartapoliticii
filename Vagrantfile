# -*- mode: ruby -*-
# vi: set ft=ruby :


# This file helps you get a development virtual machine up and running.
#    $ gem install vagrant
#    $ vagrant box add lucid32 http://files.vagrantup.com/lucid32.box
#    $ vagrant up

# Then open http://192.168.33.17/ in a browser, you should have a working site.

# For more information about vagrant, see http://vagrantup.com/

# configure your secret/db_user.php (user: root, password: root)


# TODO mount the repository in /hartapoliticii


Vagrant::Config.run do |config|
  config.vm.box = "lucid32"
  config.vm.network :hostonly, "192.168.33.17"
  config.vm.provision :shell, :path => "vagrant-provision.sh"
end
