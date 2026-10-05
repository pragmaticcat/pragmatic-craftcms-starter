# Craft Copy database guard

After running `php craft copy/setup`, add these hooks to the generated
`fortrabbit.<stage>.yaml` file, replacing `<stage>` with its filename stage:

```yaml
before:
    code/up: {  }
    db/up:
        - 'bash scripts/craft-copy/production-db-guard.sh check <stage>'
after:
    code/down:
        - 'php craft migrate/all'
        - 'php craft project-config/apply'
    db/down:
        - 'bash scripts/craft-copy/production-db-guard.sh record <stage>'
    db/up:
        - 'bash scripts/craft-copy/production-db-guard.sh record-remote <stage>'
```

The first guarded `db/up` requires a successful `db/down` to establish its
production baseline.
