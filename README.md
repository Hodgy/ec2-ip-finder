# ec2-ip-finder
Over engineered solution to find EC2 private ips

## Custom Environment Prefixes
If you want to set a prefix that is automatically applied to the environment you enter you can do so by setting the following environment variable in your chosen shell file.
```
EIF_ENV_PREFIX
```

For example if it is set to `ENV/DEV/` and you pass in `QA` it will look for the environment tag of `ENV/DEV/QA`
