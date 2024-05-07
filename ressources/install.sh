PROGRESS_FILE=/tmp/dependancy_lgWebOSTV_in_progress
if [ ! -z $1 ]; then
	PROGRESS_FILE=$1
fi
touch ${PROGRESS_FILE}
echo 0 > ${PROGRESS_FILE}
echo "********************************************************"
echo "*             Installation des dépendances             *"
echo "********************************************************"
echo 10 > ${PROGRESS_FILE}
pip install git+https://github.com/Lawouach/WebSocket-for-Python.git --upgrade
echo 50 > ${PROGRESS_FILE}
pip install wakeonlan --upgrade
echo 100 > ${PROGRESS_FILE}
echo "********************************************************"
echo "*             Installation terminée                    *"
echo "********************************************************"
rm ${PROGRESS_FILE}