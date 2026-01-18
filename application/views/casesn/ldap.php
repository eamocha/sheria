<style>
  body {
    font-family: sans-serif;
    margin: 2em;
  }
  form {
    max-width: 600px;
    margin: auto;
    padding: 2em;
    border: 1px solid #ccc;
    border-radius: 8px;
  }
  .form-group {
    margin-bottom: 1.5em;
  }
  label {
    display: block;
    font-weight: bold;
    margin-bottom: 0.5em;
  }
  input[type="text"],
  input[type="password"],
  select {
    width: 100%;
    padding: 0.8em;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box; /* Ensures padding doesn't affect width */
  }
  .tooltip {
    font-size: 0.9em;
    color: #555;
    margin-top: 0.5em;
    display: block;
  }
  .warning {
    color: #ff0000;
    font-weight: bold;
  }
  h2 {
    border-bottom: 1px solid #ccc;
    padding-bottom: 0.5em;
    margin-top: 0;
  }
</style>
<div class="container">
<form action="save_preferences.php" method="post">
  <h2>Core LDAP Connection Settings 🔌</h2>
  <p class="warning">
    These settings are essential for connecting to your LDAP server.
  </p>
  <div class="form-group">
    <label for="host">LDAP Hostname or IP Address</label>
    <input type="text" id="host" name="host" required placeholder="e.g., hd-ad-03.ca.go.ke" />
    <span class="tooltip"
      >The address of the Active Directory server.</span
    >
  </div>
  <div class="form-group">
    <label for="port">LDAP Port</label>
    <input
      type="text"
      id="port"
      name="port"
      required
      placeholder="e.g., 636"
    />
    <span class="tooltip">Use **636** for secure connections (LDAPS) or **389** for insecure.</span>
  </div>
  <div class="form-group">
    <label for="adminUser">Admin Username</label>
    <input
      type="text"
      id="adminUser"
      name="username"
      required
      placeholder="e.g., ilcms@ca.go.ke"
    />
    <span class="tooltip"
      >The username of the account used to perform searches. **Use the full email-style address (UPN)**.</span
    >
  </div>
  <div class="form-group">
    <label for="adminPassword">Admin Password</label>
    <input type="password" id="adminPassword" name="password" required />
    <span class="tooltip">The password for the admin username. This will be encrypted.</span>
  </div>
  <div class="form-group">
    <label for="baseDn">Base DN (Distinguished Name)</label>
    <input
      type="text"
      id="baseDn"
      name="base_dn"
      required
      placeholder="e.g., DC=ca,DC=go,DC=ke"
    />
    <span class="tooltip"
      >The starting point for all user searches in your directory.</span
    >
  </div>
  <div class="form-group">
    <label for="secureConnection">Secure Connection (LDAPS)</label>
    <select id="secureConnection" name="secure_connection">
      <option value="true">Yes</option>
      <option value="false">No (Insecure)</option>
    </select>
    <span class="tooltip">Connect using a secure, encrypted protocol. This should be enabled if your port is set to **636**.</span>
  </div>
  <hr />
  <h2>User and Group Management Settings 👥</h2>
  <div class="form-group">
    <label for="emailMapping">Email Mapping</label>
    <select id="emailMapping" name="emailMappingOption">
      <option value="mail">Use standard 'mail' attribute</option>
      <option value="userprincipalname">Use 'userPrincipalName' (UPN)</option>
    </select>
    <span class="tooltip"
      >Select which LDAP attribute contains the user's email address.</span
    >
  </div>
  <div class="form-group">
    <label for="userSearchBase">User Search Base DN (Optional)</label>
    <input
      type="text"
      id="userSearchBase"
      name="userSearchBase"
      placeholder="e.g., OU=Users,DC=ca,DC=go,DC=ke"
    />
    <span class="tooltip"
      >An optional, more specific starting point for user searches (e.g., to
      limit to a single department).</span
    >
  </div>
  <div class="form-group">
    <label for="userFilter">User Search Filter (Advanced)</label>
    <input
      type="text"
      id="userFilter"
      name="userFilter"
      placeholder="e.g., (sAMAccountName=%s)"
    />
    <span class="tooltip"
      >The raw LDAP filter used to find users. Use **%s** as a placeholder for the username.</span
    >
  </div>
  <div class="form-group">
    <label for="groupAttribute">Group Membership Attribute</label>
    <input
      type="text"
      id="groupAttribute"
      name="groupAttribute"
      placeholder="e.g., memberOf"
    />
    <span class="tooltip"
      >The attribute that lists the groups a user belongs to.</span
    >
  </div>
  <hr />
  <button type="submit" style="padding: 1em 2em; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
    Save Settings
  </button>
</form>
</div>