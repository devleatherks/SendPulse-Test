git-push: git-add-all git-commit-dev git-pull
	git status

git-add-all: 
	git add .

git-commit-dev: 
	git commit -m "dev"

git-pull: git-pull-all
	git status
	
git-pull-all:
	git pull origin master

