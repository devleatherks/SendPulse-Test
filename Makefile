git-push: git-pull git-add-all git-commit-dev git-push-master git-pull-remote
	git status

git-add-all: 
	git add .

git-push-master: 
	git push origin mastar

git-commit-dev: 
	git commit -m "dev"

git-pull: git-pull-all
	git status
	
git-pull-all:
	git pull origin master

git-pull-remote:
	ssh -i key.ssh root@185.65.246.248 /var/www/tooldev.top/html/dev-make.sh