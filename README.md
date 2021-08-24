# security_k8s
setup k8s cluster lab
## setup 2 ubuntu servers one is master and the other is worker node  then make sure both in the same network

After setup both servers run this command to setup docker , k8s commands run this file as sudo

```bash
#!/bin/bash
apt update
apt install docker.io
systemctl enable docker
curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | apt-key add
apt-add-repository "deb http://apt.kubernetes.io/ kubernetes-xenial main"
apt -y update
apt install -y kubeadm kubectl kubelet kubernetes-cni
```

# disable swap

```bash
sudo swapoff -a
sudo sed -i '/ swap / s/^/#/' /etc/fstab
```

# after disable swap

```bash
sudo vi /usr/lib/systemd/system/docker.service
```

# and modify the docker service to this lines

```bash
ExecStart=/usr/bin/dockerd --exec-opt native.cgroupdriver=systemd

ExecReload=/bin/kill -s HUP $MAINPID
```

# after modifying the service you should update daemon services and restart docker, kubelet services

```bash
sudo systemctl daemon-reload
sudo systemctl restart docker
sudo systemctl restart kubelet
```