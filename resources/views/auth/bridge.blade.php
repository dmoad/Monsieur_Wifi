<!DOCTYPE html>
<html>
<head><title>Redirecting...</title></head>
<body>
<script>
    // Store token first so API calls work
    const token = @json($token);
    const fallbackUser = @json($userJson);
    localStorage.setItem('mrwifi_auth_token', token);

    // Fetch real role and org info from authz service before storing user
    const headers = { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' };

    Promise.all([
        fetch('/api/auth/me', { headers }).then(r => r.ok ? r.json() : null),
        fetch('/api/organizations', { headers }).then(r => r.ok ? r.json() : null)
    ])
    .then(([me, orgsData]) => {
        const user = JSON.parse(fallbackUser);

        if (me && me.role) {
            user.role = me.role;
        }

        // Store org context
        if (orgsData && orgsData.organizations) {
            user.organizations = orgsData.organizations;
            user.current_org_id = orgsData.current_id;
            if (orgsData.current_id) {
                const currentOrg = orgsData.organizations.find(o => o.id == orgsData.current_id);
                if (currentOrg) {
                    user.current_org_name = currentOrg.name;
                    user.role = currentOrg.role;
                }
            }
        }

        localStorage.setItem('mrwifi_user', JSON.stringify(user));
        window.location.href = '/{{ $locale }}/dashboard';
    })
    .catch(() => {
        // Fallback: use Zitadel role if authz is unreachable
        localStorage.setItem('mrwifi_user', fallbackUser);
        window.location.href = '/{{ $locale }}/dashboard';
    });
</script>
</body>
</html>
