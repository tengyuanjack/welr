#! -*- coding:utf-8 -*-

# 使用结巴分词分中文
# 读入是一个json
from __future__ import unicode_literals
import simplejson as json
import jieba, sys


import jieba, sys, chardet, locale

reload(sys)
sys.setdefaultencoding('UTF-8')

if len(sys.argv) < 2:
    print "缺少参数: 应带一个json参数"
    exit(1)

jieba.load_userdict("/var/www/html/thinkphp/Public/python/partition/newdict.txt")

oriJsonStr = sys.argv[1]

# 过滤停用词
stopwords = []
with open("/var/www/html/thinkphp/Public/python/partition/stopwords.txt", 'r') as f:
    line = f.readline()
    while line:
        stopwords.append(line.strip().decode("utf-8"))
        line = f.readline()

oriDic = json.loads(oriJsonStr)
resultDic = {}
for key in oriDic:
    seg_list = jieba.cut(oriDic[key], cut_all=False)
    res = [word for word in seg_list if word not in stopwords]
    resultDic[key] = " ".join(res)

print json.dumps(resultDic, ensure_ascii=False)