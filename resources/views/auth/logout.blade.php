<!DOCTYPE html>
<html>
<head><title>Logging out...</title></head>
<body>
<script>
    // Clear auth data from localStorage before Zitadel logout
    var savedLang = localStorage.getItem('preferred_language');
    localStorage.removeItem('mrwifi_auth_token');
    localStorage.removeItem('mrwifi_user');
    localStorage.removeItem('profile_picture');
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    localStorage.removeItem('access_token');
    if (savedLang) localStorage.setItem('preferred_language', savedLang);
    window.location.href = @json($redirectUrl);
</script>
</body>
</html>
