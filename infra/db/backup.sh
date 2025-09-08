#!/bin/bash
set -euo pipefail

dbUser="root"
dbPass="root"
dbName="eskua_db"
timeStamp="$(date +%F_%H-%M-%S)"

backupDir="/var/backups/database"
out="${backupDir}/${dbName}_${timeStamp}.sql.gz"

# Dump directo
mysqldump -u"$dbUser" -p"$dbPass" "$dbName" | gzip -9 > "$out"

if [ $? -eq 0 ]; then
    echo "$(date '+%F %T') Backup finalizado correctamente. Archivo: $out"
else
    echo "$(date '+%F %T') Error durante el backup de ${dbName}"
    exit 1
fi

# Elimina backups viejos (+10 días)
find "$backupDir" -type f -name "${dbName}_*.sql.gz" -mtime +10 -delete
