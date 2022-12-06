# security_k8s
setup k8s cluster lab
### setup 2 ubuntu servers one is master and the other is worker node  then make sure both in the same network

After setup both servers run this command to setup docker , k8s commands run this file as sudo

```bash
# url : https://computingforgeeks.com/install-kubernetes-cluster-ubuntu-jammy/ ,
# url : https://computingforgeeks.com/install-mirantis-cri-dockerd-as-docker-engine-shim-for-kubernetes/
# url : https://docs.docker.com/engine/install/ubuntu/
#!/bin/bash
apt update
apt install docker.io
systemctl enable docker
sudo curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | sudo apt-key add -
curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | apt-key add
apt-add-repository "deb http://apt.kubernetes.io/ kubernetes-xenial main"
apt -y update
sudo apt-get update && sudo apt-get install -y apt-transport-https curl
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

#### now you need to setup flannel  networking model
in the master type

```bash
kubectl apply -f https://raw.githubusercontent.com/coreos/flannel/master/Documentation/kube-flannel.yml
```

#### then you need to build and push api , webapp image to your docker hub after build deploy this image to the cluster

```bash
docker build -t <docker_hub_id>/sec_webapp ./webapp_image/
docker build -t <docker_hub_id>/sec_api ./api_image/
docker login
docker push <docker_hub_id>/sec_webapp
docker push <docker_hub_id>/sec_api
kubectl apply -f ./k8s_test_images
```
# for centos or redhat url https://computingforgeeks.com/install-kubernetes-cluster-on-centos-with-kubeadm/

```bash
# Install packages
sudo yum install -y yum-utils device-mapper-persistent-data lvm2
sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
sudo yum install docker-ce docker-ce-cli containerd.io

# Create required directories
sudo mkdir /etc/docker
sudo mkdir -p /etc/systemd/system/docker.service.d

# Create daemon json config file
sudo tee /etc/docker/daemon.json <<EOF
{
  "exec-opts": ["native.cgroupdriver=systemd"],
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "100m"
  },
  "storage-driver": "overlay2",
  "storage-opts": [
    "overlay2.override_kernel_check=true"
  ]
}
EOF

# Start and enable Services
sudo systemctl daemon-reload 
sudo systemctl restart docker
sudo systemctl enable docker
```

# for v1.25.4 k8s lets setup kubeadm in 2 servers ubuntu ARM64

## master node
1. install docker
```bash
# Add repo and Install packages
sudo apt install -y  gnupg2 software-properties-common apt-transport-https ca-certificates curl lsb-release git wget
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt update 
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-compose-plugin
# Create required directories
sudo mkdir -p /etc/systemd/system/docker.service.d

# Create daemon json config file
sudo tee /etc/docker/daemon.json <<EOF
{
  "exec-opts": ["native.cgroupdriver=systemd"],
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "100m"
  },
  "storage-driver": "overlay2"
}
EOF

# Start and enable Services
sudo systemctl daemon-reload 
sudo systemctl restart docker
sudo systemctl enable docker

# Configure persistent loading of modules
sudo tee /etc/modules-load.d/k8s.conf <<EOF
overlay
br_netfilter
EOF

# Ensure you load modules
sudo modprobe overlay
sudo modprobe br_netfilter

# Set up required sysctl params
sudo tee /etc/sysctl.d/kubernetes.conf<<EOF
net.bridge.bridge-nf-call-ip6tables = 1
net.bridge.bridge-nf-call-iptables = 1
net.ipv4.ip_forward = 1
EOF
########### For Docker Engine you need a shim interface. You can install Mirantis cri-dockerd as covered in the guide below #####
# url : https://computingforgeeks.com/install-mirantis-cri-dockerd-as-docker-engine-shim-for-kubernetes/
systemctl status docker
VER=$(curl -s https://api.github.com/repos/Mirantis/cri-dockerd/releases/latest|grep tag_name | cut -d '"' -f 4|sed 's/v//g')
echo $VER
### For Intel 64-bit CPU ###
wget https://github.com/Mirantis/cri-dockerd/releases/download/v${VER}/cri-dockerd-${VER}.amd64.tgz
tar -xvf cri-dockerd-${VER}.amd64.tgz
sudo mv cri-dockerd/cri-dockerd /usr/local/bin/
### For ARM 64-bit CPU ###
wget https://github.com/Mirantis/cri-dockerd/releases/download/v${VER}/cri-dockerd-${VER}.arm64.tgz
tar -xvf cri-dockerd-${VER}.arm64.tgz
sudo mv cri-dockerd/cri-dockerd /usr/local/bin/
cri-dockerd --version
wget https://raw.githubusercontent.com/Mirantis/cri-dockerd/master/packaging/systemd/cri-docker.service
wget https://raw.githubusercontent.com/Mirantis/cri-dockerd/master/packaging/systemd/cri-docker.socket
sudo mv cri-docker.socket cri-docker.service /etc/systemd/system/
sudo sed -i -e 's,/usr/bin/cri-dockerd,/usr/local/bin/cri-dockerd,' /etc/systemd/system/cri-docker.service

sudo systemctl daemon-reload
sudo systemctl enable cri-docker.service
sudo systemctl enable --now cri-docker.socket

systemctl status cri-docker.socket
# sudo kubeadm config images pull --cri-socket /run/cri-dockerd.sock
```
2. install Install kubelet, kubeadm and kubectl
```bash
sudo apt install curl apt-transport-https -y
curl -fsSL  https://packages.cloud.google.com/apt/doc/apt-key.gpg|sudo gpg --dearmor -o /etc/apt/trusted.gpg.d/k8s.gpg
curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | sudo apt-key add
sudo apt-add-repository "deb http://apt.kubernetes.io/ kubernetes-xenial main"
sudo apt update
sudo apt install wget curl vim git kubelet kubeadm kubectl -y
sudo apt-mark hold kubelet kubeadm kubectl
kubectl version --client && kubeadm version
```
3. Disable Swap Space
```bash
sudo swapoff -a 
# Check if swap has been disabled by running the free command.
free -h
# Now disable Linux swap space permanently in /etc/fstab. Search for a swap line and add # (hashtag) sign in front of the line.
sudo vim /etc/fstab # output
# /swapfile                                 none            swap    sw              0       0
# Enable kernel modules
sudo modprobe overlay
sudo modprobe br_netfilter

# Add some settings to sysctl
sudo tee /etc/sysctl.d/kubernetes.conf<<EOF
net.bridge.bridge-nf-call-ip6tables = 1
net.bridge.bridge-nf-call-iptables = 1
net.ipv4.ip_forward = 1
EOF

# Reload sysctl
sudo sysctl --system
```

4. Initialize control plane (run on first master node)
```bash
lsmod | grep br_netfilter
sudo systemctl enable kubelet
sudo kubeadm config images pull  --cri-socket /run/cri-dockerd.sock
sudo kubeadm init  --pod-network-cidr=10.244.0.0/16  --cri-socket /run/cri-dockerd.sock
# Runtime	Path to Unix domain socket
# Docker	/run/cri-dockerd.sock
# containerd	/run/containerd/containerd.sock
# CRI-O	/var/run/crio/crio.sock
mkdir -p $HOME/.kube
sudo cp -i /etc/kubernetes/admin.conf $HOME/.kube/config
sudo chown $(id -u):$(id -g) $HOME/.kube/config
kubectl cluster-info

```


## worker node
```bash
# same step plus
kubeadm join 192.168.171.134:6443 --token kkquvw.vrc7rbzdci8683pd \
	--discovery-token-ca-cert-hash sha256:1ab978241595343c83a1135b74915ba6bf52f3e85c6448764384b45fae025c1d \
  --cri-socket /run/cri-dockerd.sock
```


