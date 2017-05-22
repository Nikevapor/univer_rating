# -*- coding: utf8 -*-
from __future__ import unicode_literals

import sys, os

from urllib import urlretrieve
from selenium import webdriver
from time import sleep
import io
import glob
import os
import shutil
import json
from pprint import pprint
import random
import string


url_login = 'http://mobsearchsd.com/?page=join&action=activate'
url_base = 'http://mobsearchsd.com/'

# местонахождение content of mobfiles.club e.g: /home/raglyamov/work/service-platform.ru/sites/local.mobfiles.club/public/content1/
project_folder = "/home/raglyamov/work/service-platform.ru/sites/local.mobfiles.club/public/content1/"

geckodriver_folder = '/home/raglyamov/Downloads/geckodriver'





firefoxProfile = webdriver.FirefoxProfile()
firefoxProfile.set_preference("browser.download.manager.showWhenStarting", False)
firefoxProfile.set_preference("browser.helperApps.neverAsk.saveToDisk","application/octet-stream")
browser_music = webdriver.Firefox(firefox_profile = firefoxProfile, executable_path = geckodriver_folder)

# browser_category = webdriver.PhantomJS()
browser_music.get(url_login)


# авторизация на сайт
login_form = browser_music.find_element_by_xpath(".//input[@name='login']")
pass_form = browser_music.find_element_by_xpath(".//input[@name='password']")
button_form = browser_music.find_element_by_xpath(".//input[@type='submit']")
login_form.send_keys("mfb06018")
pass_form.send_keys("27594")
button_form.click()
sleep(1)

#путь куда будет качаться ['windows', 'android'], ['games', 'soft]
destination = "/home/raglyamov/work/service-platform.ru/sites/local.mobfiles.club/public/content1/windows/soft/public/"

# скачивание файлов
with open('/home/raglyamov/work/service-platform.ru/sites/local.mobfiles.club/public/content1/windows/soft/data.json') as data_file:
    data = json.load(data_file)


for genres in data:
    for games in data[genres]:
        try:
            browser_music.get('http://mobsearchsd.com/' + games['link'])
            browser_music.find_element_by_xpath(".//a[@class='button']").click()
            sleep(3)
            while True:
                result = str(max(glob.iglob('/home/raglyamov/Downloads/*'), key=os.path.getctime)).split('.')
                print result
                if 'part' in result:
                    sleep(5)
                elif 'xap' in result:
                    break
                else:
                    sleep(5)
            # sleep(20)
            new_id = ''.join(random.choice(string.ascii_uppercase + string.digits) for i in range(10))
            games['id'] = new_id
            current_path = str(max(glob.iglob('/home/raglyamov/Downloads/*.xap'), key=os.path.getctime))
            os.rename(current_path, destination + new_id + ".xap")
            sleep(1)
            image = browser_music.find_element_by_xpath(".//div[@class='img-wrp']/img").get_attribute('src')
            urlretrieve(image, "/home/raglyamov/work/service-platform.ru/sites/local.mobfiles.club/public/content1/windows/soft/public/images/" + games['id'] + ".jpg")
            sleep(1)
        except BaseException:
            print ('Ошибка в ' + games['link'])

with open('data.json', 'w') as outfile:
    json.dump(data, outfile)


browser_music.close()
