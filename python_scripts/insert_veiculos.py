import mysql.connector
import time
import random
from selenium import webdriver
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
from selenium.common.exceptions import NoSuchElementException, TimeoutException
import re
import uuid
from selenium.common.exceptions import TimeoutException

# Configuração do banco de dados.
config = {
    'user': 'root',
    'password': '',
    'port': 3306, # Atualize de acordo com sua porta 
    'host': '127.0.0.1',
    'database': 'root',
    'raise_on_warnings': True,
}

# Inicialização do WebDriver
service = Service(ChromeDriverManager().install())
driver = webdriver.Chrome(service=service)

try:
    cnx = mysql.connector.connect(**config)
    cursor = cnx.cursor()

    for _ in range(10):  # Repetir o processo 10 vezes
        driver.get('https://veiculos.fipe.org.br/')
        time.sleep(5)  

        WebDriverWait(driver, 30).until(
            EC.element_to_be_clickable((By.XPATH, "//div[@class='title' and contains(text(), 'Consulta de Carros e Utilitários')]"))
        ).click()
        time.sleep(5)

        # Seleciona uma marca aleatoriamente
        WebDriverWait(driver, 20).until(
            EC.element_to_be_clickable((By.XPATH, '//*[@id="selectMarcacarro_chosen"]/a'))
        ).click()
        opcoes_marca_xpath = '//*[@id="selectMarcacarro_chosen"]/div/ul/li'
        WebDriverWait(driver, 20).until(
            EC.visibility_of_element_located((By.XPATH, opcoes_marca_xpath))
        )
        opcoes_marca = driver.find_elements(By.XPATH, f"{opcoes_marca_xpath}[position() <= 90]")
        if opcoes_marca:
            random.choice(opcoes_marca).click()
        else:
            print("Nenhuma marca encontrada ou a lista de marcas é menor que o esperado.")

        # Espera pela atualização da lista de modelos
        WebDriverWait(driver, 20).until(
            EC.element_to_be_clickable((By.XPATH, '//*[@id="selectAnoModelocarro_chosen"]/a'))
        ).click()
        modelo_xpath = '//*[@id="selectAnoModelocarro_chosen"]/div/ul/li[1]'
        WebDriverWait(driver, 20).until(
            EC.element_to_be_clickable((By.XPATH, modelo_xpath))
        ).click()

        # Seleciona um ano aleatoriamente ou a única opção disponível
        WebDriverWait(driver, 20).until(
            EC.element_to_be_clickable((By.XPATH, '//*[@id="selectAnocarro_chosen"]/a'))
        ).click()

        opcoes_ano = WebDriverWait(driver, 20).until(
            EC.presence_of_all_elements_located((By.XPATH, '//*[@id="selectAnocarro_chosen"]/div/ul/li'))
        )

        if len(opcoes_ano) > 1:
            random.choice(opcoes_ano[1:]).click()  # Assume que o primeiro é um placeholder
        else:
            opcoes_ano[0].click()  # Escolhe a única opção disponível


        # Clicar no botão "Pesquisar"
        WebDriverWait(driver, 20).until(
            EC.element_to_be_clickable((By.ID, 'buttonPesquisarcarro'))
        ).click()
        WebDriverWait(driver, 20).until(
            EC.presence_of_element_located((By.ID, "resultadoConsultacarroFiltros"))
        )

        # Extração de informações
        container_resultados = driver.find_element(By.ID, "resultadoConsultacarroFiltros")
        nome = container_resultados.find_element(By.CSS_SELECTOR, 'tr:nth-child(4) td:nth-child(2)').text
        codigo_fipe = container_resultados.find_element(By.CSS_SELECTOR, 'tr:nth-child(2) td:nth-child(2)').text
        preco = container_resultados.find_element(By.CSS_SELECTOR, 'tr.last td:nth-child(2)').text

        # Formatação do preço e inserção no banco de dados
        preco_formatado = re.sub(r"[^0-9,]", "", preco).replace(",", ".")
        preco_decimal = float(preco_formatado)
        id_gerado = str(uuid.uuid4())
        add_veiculo = ("INSERT INTO veiculos "
                       "(id, nome, codigo_fipe, preco) "
                       "VALUES (%s, %s, %s, %s)")
        data_veiculo = (id_gerado, nome, codigo_fipe, preco_decimal)
        cursor.execute(add_veiculo, data_veiculo)
        cnx.commit()

except mysql.connector.Error as err:
    print(f"Falha na inserção de dados: {err}")
finally:
    if cursor is not None:
        cursor.close()
    if cnx is not None:
        cnx.close()
    driver.quit()
