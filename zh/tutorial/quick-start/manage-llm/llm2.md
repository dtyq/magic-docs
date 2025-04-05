## 一、功能预览
麦吉地址后面加admin路由，例如访问地址：[https://magic.xxx.com/admin/ai/model](https://magic.xxx.com/admin/ai/model)

![Fi-lguvpG_yCUWav8wIsw6lfvoYp.png](/static/img/Fi-lguvpG_yCUWav8wIsw6lfvoYp.png)




## 二、配置微软 arzure服务商
#### 2.1api地址
[https://kobayashi-aoai-eastus2-prod.openai.azure.com](https://kobayashi-aoai-eastus2-prod.openai.azure.com)

![FkV9KpWFwEU56B88zPK7nwdUeYil.png](/static/img/FkV9KpWFwEU56B88zPK7nwdUeYil.png)




## 三、配置字节服务商
#### 3.1 api地址：
[https://ark.cn-beijing.volces.com/](https://ark.cn-beijing.volces.com/)

![FioaPOfC3oc0bGaaWq78sgyd7_i1.png](/static/img/FioaPOfC3oc0bGaaWq78sgyd7_i1.png)



#### 3.2 模型名称

![Fn2GBfAUgc4AkK5SiGbmYj3x8kgj.png](/static/img/Fn2GBfAUgc4AkK5SiGbmYj3x8kgj.png)





## 四、配置自定义服务商（支持OpenAi api形式）
#### 4.1添加自定义服务商

![FvKLaK0LaSNSMYsVLDmpRBGbJ7Ad.png](/static/img/FvKLaK0LaSNSMYsVLDmpRBGbJ7Ad.png)



#### 4.2配置api地址
Kimi大模型Api地址：[https://api.moonshot.cn](https://api.moonshot.cn)
百度千帆平台Api地址：[https://qianfan.baidubce.com/v2](https://qianfan.baidubce.com/v2)

![FtB1mFoFCiL3kAaqBh0x8yAYnBw8.png](/static/img/FtB1mFoFCiL3kAaqBh0x8yAYnBw8.png)



#### 4.3添加对应的模型

![Fnf25GBXzCb_gnS1DtLSDLJOk-LR.png](/static/img/Fnf25GBXzCb_gnS1DtLSDLJOk-LR.png)




## 五、开通向量化embedding模型
火山云为例：
5.1 进入向量化embedding模型介绍：[https://www.volcengine.com/docs/82379/1302003](https://www.volcengine.com/docs/82379/1302003)
5.2 点击：去调试


![FsqFvaxlu7MiQB7Jg8zsgmlyBoeU.png](/static/img/FsqFvaxlu7MiQB7Jg8zsgmlyBoeU.png)




5.3 发起调试，如果没用开通，会要求开通模型，并要求授权 API Key


![FrLzQgYd-R0cPuh-grhOffzgNyLR.png](/static/img/FrLzQgYd-R0cPuh-grhOffzgNyLR.png)




5.4 调试成功之后，调用magic接口修改emberding模型(临时用，找小培)，后面会改到admin配置面板