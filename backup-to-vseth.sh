# backup source to VSETH deploy repository
git clone git@gitlab.ethz.ch:vseth/sip-com-apps/musikzimmer-pay.git deploy
mkdir -p ./deploy/source
git bundle create ./deploy/source/source.git-bundle --all
git --git-dir=./deploy/.git --work-tree=./deploy add ./source/source.git-bundle
git --git-dir=./deploy/.git commit -m "update source bundle"
git --git-dir=./deploy/.git push
rm -rf deploy
