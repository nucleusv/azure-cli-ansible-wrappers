
## Azure CLI Ansible wrappers




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