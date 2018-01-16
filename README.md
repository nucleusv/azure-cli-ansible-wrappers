
## Azure CLI (2.x) wrappers for Ansible

### How to build 

```bash
   # Clone the repo
   git clone https://github.com/nucleusv/azure-cli-ansible-wrappers.git

   # Update autogenerated Azure CLI documentation 
   git submodule init
   git submodule update

   # Run script which will generate wrappers. 
   # Tested with PHP 7, YAML extension must be installed
   ./generate-wrappers.php 


```

#### Issues
   The word 'tags' can not be used as parameter because it is reserved variable name in Ansible, 
   that is why it is replaced with 'rtags'




### How to use in playbooks
```yaml
   - include_tasks: az-wrappers/az-sql-server-create.yml
     with_items:      
       - { 
           'admin-password': "{{ adminpwd.stdout }}",
           'admin-user': "adminsql",
           'location': "{{ az_region }}",
           'name': "sql server name",
           'resource-group': "{{ resource_group_name }}"   
         }

   - include_tasks: az-wrappers/az-sql-elastic-pool-create.yml
     with_items:
       - { 
           'name': "elastic-pool-name" ,                  
           'server': "{{ az_sql_server_create.output['name'] }}" ,
           'resource-group':  "{{ resource_group_name }}"
         }
   
   - include_tasks: az-wrappers/az-sql-db-create.yml
     with_items:
       - { 'name': "test-db-1" ,                  
           'server': "{{ az_sql_server_create.output['name'] }}" ,
           'resource-group':  "{{ resource_group_name }}"
         }
       - { 'name': "test-db-2" ,                  
           'server': "{{ az_sql_server_create.output['name'] }}" ,
           'resource-group':  "{{ resource_group_name }}"
         }
```