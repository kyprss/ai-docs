# AI Docs

AI Docs is an easy tool to download the latest documentation from a git repository.
It creates a reference file which you can use in your AI guidelines.

## Installation

Install via Composer:

```bash
composer require kyprss/ai-docs
```

## Usage

```
/vendor/bin/ai-docs
```

### Initialization

```
/vendor/bin/ai-docs init
```

On the first run a config file `ai-docs.json` will be created in the root directory of your project.

The file will be look like this:

```
{
    "config": {
        "target_path": ".ai/docs/"    
    },
    "sources": {
        "laravel": {
            "type": "repository",
            "url": "git@github.com:laravel/docs.git",
            'branch' => '12.x',
            "files": ["*.md"]
        }
    }
}
```

### Sync

```
/vendor/bin/ai-docs sync
```

This command will sync all given sources by doing the following steps:

- downloading the latest files from the git repo to temp directory
- matching all files with the given patterns
- copy the found files to `{target_path}/{name}/docs`
- creates a reference file `{target_path}/{name}/{name}.md` which links to the files