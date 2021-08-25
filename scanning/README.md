# scanning the cluster

#### lets first misconfigure the cluster
type in the master

```bash
kubectl create clusterrolebinding encdecservice --clusterrole cluster-admin --serviceaccount=default:default
```

#### lets begin with nmap scan in master , worker nodes i used kali linux

```bash
sudo nmap 192.168.232.150,151  -p- -sV -oA k8s_nmap_scan.txt
```