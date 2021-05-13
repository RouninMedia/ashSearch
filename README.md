# ashSiteSearch
The SiteSearch Module for ash


## ashSiteSearch Search Filters


### Example 1:

    {"Exclude_Folders":{"de":{},"es":{},"fr":{},"ru":{},"safety-data-sheets":{"Include_Folders":{"/":{}}}}}
    
**Explanation:**

### Example 2:

    {"Include_Folders":{"de":{"Exclude_Folders":{"sicherheitsdatenblätter":{"Include_Folders":{"/":{}}}}}}}
    
**Explanation:**
    
### Example 3:

    {"Include_Folders":{"es":{"Exclude_Folders":{"hojas-de-datos-de-seguridad":{"Include_Folders":{"/":{}}}}}}}
    
**Explanation:**

### Example 4:

    {"Include_Folders":{"nail-products":{},"es":{}}}
    
**Explanation:**
