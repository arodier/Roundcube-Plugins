# Put your local configuration in 'local' folder
include local/*.mk

### Variable settings #######################################################
# default folder for roundcube plugins. You can override in a local makefile.
pluginsDir ?= /var/www/roundcube-trunk/plugins

# Install all plugins in the dev folder
all: identiteam

# clean packages
clean:
	@rm -f packs/*tgz

# Create a tgz package for each plugin
packs: clean
	@cd plugins
	@git archive --format tar --prefix=identiteam/ HEAD:plugins/identiteam | gzip -9 >packs/identiteam.tgz 

# install packages in the currently defined plugins folder
identiteam: packs
	@tar zxvf packs/identiteam.tgz -C $(pluginsDir) 

