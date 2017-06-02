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
    # 'Kazan Federal University': 'kpfu.ru',
    # 'National Research Nuclear University MEPhI': 'mephi.ru',
    # 'Massachusetts Institute of Technology': "mit.edu"
    # 'University of Lisbon': 'ulisboa.pt',
    # 'Indian Institute of Science': 'iisc.ernet.in',
    # 'University of Buenos Aires': 'uba.ar',
    # 'Sungkyunkwan University': 'skku.edu',
}
output_data = {}
for university_title in data:
    print university_title
    max = 0
    univer_data = []
    workers_data = {}
    exit_from_loop = False
    browser_music.get(url_base)
    sleep(1)
    search_form = browser_music.find_element_by_xpath(".//input[@class='gs_in_txt']")
    button_form = browser_music.find_element_by_xpath(".//button[@id='gs_hp_tsb']")
    search_form.send_keys(university_title)
    button_form.click()
    sleep(1)
    browser_music.find_element_by_xpath(".//div[@class='gs_ob_inst_r']/a").click()
    sleep(1.2)
    while (([] != browser_music.find_elements_by_xpath(".//div[@class='gsc_1usr_text']/div[@class='gsc_1usr_cby']")) and
               (exit_from_loop == False)):

        try:
            page = browser_music.current_url
            print page
            names = browser_music.find_elements_by_xpath(".//h3[@class='gsc_1usr_name']/a")
            links = []
            for name in names:
                links.append(name.get_attribute('href'))
            for link in links:
                browser_music.get(link)
                sleep(2)
                worker_name = browser_music.find_element_by_xpath(".//div[@id='gsc_prf_in']").get_attribute('innerHTML')
                index_cit = browser_music.find_element_by_xpath(".//th[@class='gsc_rsb_sc1']/a").click()
                sleep(0.5)
                years = browser_music.find_elements_by_xpath(".//div[@id='gsc_md_hist_b']/span[@class='gsc_g_t']")
                years_cit = browser_music.find_elements_by_xpath(".//div[@id='gsc_md_hist_b']/a[@class='gsc_g_a']/span")

                worker_common = {}
                worker_stat = {}
                overall = {}
                last_five = {}
                rows = browser_music.find_elements_by_xpath("(.//table[@id='gsc_rsb_st']//tr)[position() > 1]")
                overall['cit_num'] = rows[0].find_elements_by_tag_name('td')[1].get_attribute('innerHTML')
                if (max < int(overall['cit_num'])):
                    max = int(overall['cit_num'])
                if (int(overall['cit_num']) < 25):
                    exit_from_loop = True
                    break
                last_five['cit_num'] = rows[0].find_elements_by_tag_name('td')[2].get_attribute('innerHTML')
                overall['h_index'] = rows[1].find_elements_by_tag_name('td')[1].get_attribute('innerHTML')
                last_five['h_index'] = rows[1].find_elements_by_tag_name('td')[2].get_attribute('innerHTML')
                overall['i10_index'] = rows[2].find_elements_by_tag_name('td')[1].get_attribute('innerHTML')
                last_five['i10_index'] = rows[2].find_elements_by_tag_name('td')[2].get_attribute('innerHTML')
                worker_stat['overall'] = overall
                worker_stat['last_five'] = last_five
                worker_common['stats'] = worker_stat

                worker_cites = {}
                for year, cites in zip(years, years_cit):
                    worker_cites[year.get_attribute('innerHTML')] = cites.get_attribute('innerHTML')
                worker_common['cites'] = worker_cites
                workers_data[worker_name] = worker_common
            browser_music.get(page)
            sleep(2)
            if (browser_music.find_element_by_xpath(".//button[@class='gs_btnPR gs_in_ib gs_btn_half gs_btn_srt']") is None):
                break
            if (exit_from_loop == False):
                browser_music.find_element_by_xpath(".//button[@class='gs_btnPR gs_in_ib gs_btn_half gs_btn_srt']").click()
                sleep(2)
        except BaseException:
            print ('Ошибка в ' + university_title + worker_name)


    if len(univer_data) > 0:
        if (univer_data[-1] != workers_data):
            univer_data.append(workers_data)
    else:
        univer_data.append(workers_data)

    output_data[university_title] = univer_data
    with open('data_scholar_' + university_title + '.json', 'w') as outfile:
        json.dump(output_data, outfile)

browser_music.close()

end = time()

print end - start

# Process time: 4062.9746
# 150.852161884 - mephi
# 12874.8197289 - mit
# 49298.9019611 - mit2
# 3381.18645 - mit4
# 2911.07629991 - kfu