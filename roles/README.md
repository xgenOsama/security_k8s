# Roles RABC (Role based access control )

* roles is for a specific namespace only
* rolebinding something is used for binding any role for specific  account
* first we will create a role and bind it to specific account using rolebindings
* remember this is done at namespace level

#### To implement Role Based Access Control in k8s , we should understand

* Subjects
  * The subjects specify the users who want to access the k8s api
* Resources
  * Are the k8s Api objects
  * They could be pod ,nodes , services , ... etc
  * The resources are the objects that we wanna access
* Verbs
  * Are basically the operations that can be executed in the resources
  * for example we can create a pod , delete a pod , list pods

### now in the master lets create a namespace for example rbac-ns

```bash
kubectl create namespace rbac-ns
```

### now we are going to create a service account in this namespace
#### before we create it lets ansower  what is a service account ?
when you run a command such as ```kubectl get pods``` you are authenticated by the api server as a user account
Similarly, process in containers inside a pod can also interact with cluster when they do it there should be
a mechanism to authenticate them and this is achieved using service account
a service account represents an identity for the process in the container inside the pod
in kubernetes it's possible to create a service account and assign it to pod
if no service account is explicitly  assigned to a pod it will take the default service account

```bash
kubectl apply -f service-account.yaml
kubectl get sa -n rbac-ns
kubectl apply -f role.yaml
kubectl get roles -n rbac-ns
kubectl apply -f role-bindings.yaml
kubectl get rolebindings -n rbac-ns
kubectl apply -f deployment.yaml
kubectl get deploy -n rbac-ns
```
