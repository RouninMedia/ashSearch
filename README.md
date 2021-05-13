# ashSiteSearch
The SiteSearch Module for ash


## ashSiteSearch Search Filters

**Search Filters** are the single most powerful feature of **ashSiteSearch** since they enable unlimited sculpting of which pages on the site the **SiteSearch** will search and which pages on the site the **SiteSearch** will ignore.

The **Search Filter** uses a `JSON` syntax and comprises a set of nested and alternating `Include_Folders` and `Exclude_Folders` Directives, each directive indicating the exceptions to its own parent directive. To be clear, it's worth mentioning that while `Exclude` means *"Exclude these folders"*, its counterpart `Include` means *"only Include these folders and exclude everything else"*. 

To illustrate more verbosely how the **Search Filter** is constructed:

 - Each **Search Filter** begins with either an `Include_Folders` or `Exclude_Folders` Directive
 - Each folder to be included or excluded will confirm that it requires zero exceptions to the parent directive (`{}`) if the same directive is intended to apply to all of its child folders, grandchild folders and subsequent descendant folders
 - **But**, if there *are* exceptions to the parent directive, these may be indicated by nesting a counter-directive (whichever is the contrary to the parent directive) within the curly braces, immediately followed by the next set of folders the new, exceptional, directive applies to 




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
