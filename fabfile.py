from fabric.api import *
from fabric.api import run

env.hosts = ['bildog.de']
env.user = 'u40163469'

def deploy():
    run('cd /kunden/homepages/40/d159634318/htdocs/wp-bildog-website && git pull origin master')
