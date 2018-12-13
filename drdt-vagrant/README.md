This is the DRDT Vagrant Box.

Will serve as a base box for the DRDT project.

all environnement will be based off this box.

	Ubuntu 18.04
	apache2
	php 7.1
	MySQL


Requirements/How to Install :
__________________

	What will be installed :

	Fonctional LAMP Server. 
		-apache2
		-Ubuntu 18.04
		-php 7.1
		-MySQL

	Full Wordpres installation of drdt.constructionprotips.com

	Redis cache server.

	1 ) Add 192.168.205.11 drdt.constructionprotips.com in your /etc/hosts file.

	2 ) Latest version of Vagrant is required in order to setup the private network with ubuntu 18.04.

	3 ) Latest version of VirtualBox

	4 ) The vagrant diskresize plugin is required.

		To install :

		vagrant plugin install vagrant-diskresize

	5 ) You will need a valid copy of the CPT database you can download one here :

		http://staging.constructionprotips.com/wp-content/uploads/sites/9/2018/12/CPT_dump.sql

		*Currently some issue downloading the file, I'm working on finding a good place to store this. Will probably be in one of our s3 storage on AWS.

		you will need to place that file in the drdt-vagrant/ folder
		
	7 ) cd into the folder and do the following command : vagrant up

Access information:
___________________

	IP of the box : 192.168.205.11

	To access the site :

	http://drdt.constructionprotips.com

	You can SSH into the Vagrant Box with the command :

	vagrant ssh

	MySQL credentials :

	UN : admin
	PW : password

	Wordpress user :

	UN : local.admin
	PW : password

	** PLEASE NOTE THAT CURRENTLY THE DRDT CODE IS PULLED FROM THE MASTER GIT BRANCH **


Upcomming features 
___________________

	Different env. setup : (Test, Staging, Prod).

	Build w/ specific branch/pull request from Git.

	Automatic Database download if not present in the folder.

	npm run gulp failed task (only npm install runs for the moment)

	Script to extract a copy of current working DB to local machine.

	Better Redis default configuration.

	Nginx installation.
	

If you have any questions, request or suggestion, I will be pleased to take a look a them :) just send us a request at :
devops@tmbi.com

