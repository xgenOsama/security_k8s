# security_k8s
setup k8s cluster lab
### setup 2 ubuntu servers one is master and the other is worker node  then make sure both in the same network

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

### disable swap

```bash
sudo swapoff -a
sudo sed -i '/ swap / s/^/#/' /etc/fstab
```

### after disable swap

```bash
sudo vi /usr/lib/systemd/system/docker.service
```

### and modify the docker service to this lines

```bash
ExecStart=/usr/bin/dockerd --exec-opt native.cgroupdriver=systemd

ExecReload=/bin/kill -s HUP $MAINPID
```

### after modifying the service you should update daemon services and restart docker, kubelet services

```bash
sudo systemctl daemon-reload
sudo systemctl restart docker
sudo systemctl restart kubelet
```

#### I setup two vms one for k8s master and the other is for worker lets now setup the cluster 
#### on the master vm i set an static ip for it 192.168.232.150 lets setup the master k8s

```bash
sudo kubeadm init --apiserver-advertise-address=192.168.232.150 --pod-network-cidr=10.244.0.0/16
```

#### after kubeadm init the cluster you should now configure the kubectl to connect to this cluster

```bash
mkdir $HOME/.kube
sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
sudo chown $(id -u):$(id -g) $HOME/.kube/config
echo "list test our cluster"
kubectl get nodes
```

#### now you need to configure the worker node which i set the static ip for it 192.168.232.151
#### you will get the token as a result of kubeadm command in the master node

```bash
sudo kubeadm join 192.168.232.150:6443 --token p4c6xq.e0avahyuo110d8ls --discovery-token-ca-cert-hash sha256:3926349f436b66965b92c55f17dc0a7367ed72cf025f65a0a89295b6c4dec572 --node-name=worker1
```