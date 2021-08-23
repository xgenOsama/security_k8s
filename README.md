# security_k8s
setup k8s cluster lab
## setup 2 ubuntu servers one is master and the other is worker node  then make sure both in the same network
After setup both servers run this command to setup docker , k8s commands
```bash
#!/bin/bash
apt update
apt install docker.io
curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | apt-key add
apt-add-repository "deb http://apt.kubernetes.io/ kubernetes-xenial main"
apt -y update
apt install -y kubeadm kubectl kubelet kubernetes-cni
```
