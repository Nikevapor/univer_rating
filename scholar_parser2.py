# -*- coding: utf8 -*-
from __future__ import unicode_literals

import sys, os

from urllib import urlretrieve
from selenium import webdriver
from time import sleep
from time import time
import io
import glob
import os
import shutil
import json
from pprint import pprint
import random
import string

start = time()

url_base = 'https://scholar.google.ru'

# местонахождение content of mobfiles.club e.g: /home/raglyamov/work/service-platform.ru/sites/local.mobfiles.club/public/content1/
project_folder = "/home/raglyamov/my_diplom/univer_rating"

geckodriver_folder = '/home/raglyamov/Downloads/geckodriver'

firefoxProfile = webdriver.FirefoxProfile('/home/raglyamov/.mozilla/firefox/9zrns2u3.default')
browser_music = webdriver.Firefox(firefox_profile = firefoxProfile, executable_path = geckodriver_folder)

data = {
    "Moscow Institute of Physics and Technology": "f",
    # "Harbin Institute of Technology": 'f'
    # "Gwangju Institute of Science and Technology": 'gist.ac.kr'
    # 'Harvard University': 'harvard.edu',
    # 'Lomonosov Moscow State University': 'msu.ru',
    # 'Kazan Federal University': 'kpfu.ru',
    # 'University of Lisbon': 'ulisboa.pt',
    # 'Indian Institute of Science': 'iisc.ernet.in',
    # 'University of Buenos Aires': 'uba.ar',
    # 'Sungkyunkwan University': 'skku.edu'
}
output_data = {}
for university_title in data:
    print university_title
    univer_data = []
    workers_data = {}
    browser_music.get(url_base)
    sleep(1)
    search_form = browser_music.find_element_by_xpath(".//input[@class='gs_in_txt']")
    button_form = browser_music.find_element_by_xpath(".//button[@id='gs_hp_tsb']")
    search_form.send_keys(university_title)
    button_form.click()
    sleep(2)
    browser_music.find_element_by_xpath(".//div[@class='gs_ob_inst_r']/a").click()
    sleep(1)
    links = []
    exit_from_while = False
    while (([] != browser_music.find_elements_by_xpath(".//div[@class='gsc_1usr_text']/div[@class='gsc_1usr_cby']")) and
               (browser_music.find_element_by_xpath(".//button[@class='gs_btnPR gs_in_ib gs_btn_half gs_btn_srt']").get_attribute('disabled') == None) ):
        if(int(browser_music.find_element_by_xpath(".//div[@class='gsc_1usr_text']/div[@class='gsc_1usr_cby']").get_attribute('innerHTML').split(":")[1]) < 25):
            break
        else:
            try:
                overall = {}
                page = browser_music.current_url
                print page
                names = browser_music.find_elements_by_xpath(".//h3[@class='gsc_1usr_name']/a")
                for name in names:
                    links.append(name.get_attribute('href'))
                browser_music.find_element_by_xpath(".//button[@class='gs_btnPR gs_in_ib gs_btn_half gs_btn_srt']").click()
                sleep(1)
            except BaseException:
                print ('Ошибка в ' + university_title + page)

    output_data[university_title] = links

with open('data_scholar_links_msc.json', 'w') as outfile:
    json.dump(output_data, outfile)


browser_music.close()

end = time()

print end - start

# Process time: 248.279042006
# Process time: 107.830552101