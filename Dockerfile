# Usamos uma imagem base do Python
FROM python:3.10-slim

# Definimos o diretório de trabalho
WORKDIR /app

# Copiamos o script Python para o diretório de trabalho
COPY script.py /app/script.py

# Instalamos o Chrome e o driver necessário para o Selenium
RUN apt-get update && apt-get install -y wget gnupg2 unzip \
    && wget -q -O - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add - \
    && sh -c 'echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" >> /etc/apt/sources.list.d/google.list' \
    && apt-get update \
    && apt-get install -y google-chrome-stable \
    && wget https://chromedriver.storage.googleapis.com/101.0.4951.41/chromedriver_linux64.zip \
    && unzip chromedriver_linux64.zip \
    && mv chromedriver /usr/bin/chromedriver \
    && chmod +x /usr/bin/chromedriver \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* chromedriver_linux64.zip

# Atualizamos o pip e instalamos as dependências do Python
RUN pip install --upgrade pip \
    && pip install selenium mysql-connector-python webdriver-manager

# Comando para executar o script Python quando o container iniciar
CMD ["python", "script.py"]
