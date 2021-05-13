# ashSiteSearch
The SiteSearch Module for ash.

_______

## ashSiteSearch Search Filters

**Search Filters** are the single most powerful feature of **ashSiteSearch** since they enable unlimited sculpting of which pages on the site the **SiteSearch** will search and which pages on the site the **SiteSearch** will ignore.

The **Search Filter** uses a `JSON` syntax and comprises a set of nested and alternating `Include_Folders` and `Exclude_Folders` Directives, each directive indicating the exceptions to its own parent directive.

_____

## Constructing a Search Filter
It's worth mentioning at this point that while `Exclude` means *"Exclude these folders"*, its counterpart `Include` means *"only Include these folders and exclude everything else"*. 

To illustrate in more detail how the **Search Filter** is constructed:

 - Each **Search Filter** begins with either an `Include_Folders` or `Exclude_Folders` Directive
 - Each folder to be included or excluded will confirm that it requires zero exceptions to the parent directive (`{}`) if the same directive is intended to apply to all of its child folders, grandchild folders and subsequent descendant folders
 - **But**, if there *are* exceptions to the parent directive, these may be indicated by nesting a counter-directive (whichever is the contrary to the parent directive) within the curly braces, immediately followed by the next set of folders the new, exceptional, directive applies to

This means that any **Search Filter** will *always* follow the format:

 - *Start:* **`Exclude`** folders `2`, `3` & `5` and all their descendant subfolders
 - *Except* **`Include`** subfolders `3.2` & `3.3` and all their descendant subfolders
 - *Except* **`Exclude`** subfolders `3.2.4` and all its descendant subfolders
 - *Except* **`Include`** `3.2.4.2` and all its descendant subfolders

______

## Syntax in Search Filters

**Search Filters** use *two* shorthand symbols:

 - `/` is a shorthand meaning **the `root` of this folder**
 - `*` is a shorthand meaning **all the subfolders of this folder**

Given this, we know that:

 - `{"Exclude_Folders":{"/":{}}}` means *exclude the `root` (but include all the subfolders) of this folder*
 - `{"Exclude_Folders":{"*":{}}}` means *exclude all the subfolders (but include the `root`) of this folder 
 - `{"Exclude_Folders":{"my-folder":{}}}` means *exclude `my-folder`*

and

 - `{"Include_Folders":{"/":{}}}` means *include the `root` (but exclude all the subfolders) of this folder*
 - `{"Include_Folders":{"*":{}}}` means *include all the subfolders (but exclude the `root`) of this folder
 - `{"Include_Folders":{"my-folder":{}}}` means *include `my-folder` (but exclude the `root` and **all other subfolders** of this folder)*

_______

## Examples of Search Filters


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

    {"Include_Folders":{"de":{},"es":{}}}
    
**Explanation:**
