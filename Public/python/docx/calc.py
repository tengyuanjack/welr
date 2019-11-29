#! -*- coding:utf-8 -*-

import sys, docx, codecs, os

reload(sys)
sys.setdefaultencoding('UTF-8')

if len(sys.argv) < 2:
    print "缺少参数: 应带一个文档参数"
    exit(1)


# # 无论如何，请用 linux 系统的当前字符集输出：
# if sys.stdout.encoding is None:
#     enc = os.environ['LANG'].split('.')[1]
#     sys.stdout = codecs.getwriter(enc)(sys.stdout)  # 替换 sys.stdout

docName = sys.argv[1]

doc = docx.Document(docName)
docRst = {}
levelArr = []
contentArr = []
for para in doc.paragraphs:
    contentArr.append(para.text)
    levelArr.append(para.style.name.decode("utf-8"))

# 先找最深的heading
maxDepth = 0
for level in levelArr:
    l = level.strip()
    lArr = l.split(" ")
    if len(lArr) == 2 and lArr[0] == "Heading":
        depth = int(lArr[1])
        maxDepth = max(maxDepth, depth)
maxDepth += 1

# 定义计数器，用来计数
counter = [0 for i in range(maxDepth+1)]
# 上一个level，用来给normal的定级别
lastLevel = 0
# 用来记录每一条内容的编号
numberArr = [None]*len(levelArr)
# 记录是否已经开始有Heading， 目的是用来给一开始的Normal内容编号为0.0.0...
isOccurHeading = False
for i in range(len(levelArr)):
    l = levelArr[i].strip()
    s = contentArr[i].strip()
    lArr = l.split(" ")
    if len(lArr) == 1 and lArr[0] == "Normal":
        if isOccurHeading:
            counter[lastLevel+1] += 1
    elif len(lArr) == 2 and lArr[0] == "Heading":
        isOccurHeading = True
        depth = int(lArr[1])
        lastLevel = depth
        counter[depth] += 1
        for j in range(depth+1, len(counter)):
            counter[j] = 0
    # 拼接编号
    newCounter = counter[1:]
    numberArr[i] = ".".join(map(str, newCounter))

for i in range(len(levelArr)):
    if numberArr[i].strip() == '' or contentArr[i].strip() == '':
        continue
    print numberArr[i] + "###" + levelArr[i] + "###" + contentArr[i]





