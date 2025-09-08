FROM mysql:8.0

# Instalar cron y bash
RUN apt-get update && apt-get install -y \
    cron \
    bash \
    && rm -rf /var/lib/apt/lists/*

# Crear directorios necesarios
RUN mkdir -p /var/backups/database /var/log/database /opt/eskua/scripts

# Copiar script de backup
COPY backup.sh /opt/eskua/scripts/eskua_db_backup.sh
RUN chmod +x /opt/eskua/scripts/eskua_db_backup.sh

# Copiar crontab
COPY crontab.txt /etc/cron.d/eskua-cron
RUN chmod 0644 /etc/cron.d/eskua-cron && crontab /etc/cron.d/eskua-cron

# Configurar que se arranque cron + mysql juntos
CMD service cron start && docker-entrypoint.sh mysqld