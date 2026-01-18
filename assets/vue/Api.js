export default {
  getInitialHeaders(type, accept) {
    let headers = {
      "Authorization": "Bearer " + localStorage.getItem("api-access-token"),
      "Content-Type": type ?? "application/json",
      "Accept": accept ?? "application/json",
      "User-Language": userLanguage ?? "english",
    };
    return { headers };
  },
  getApiBaseUrl(module='core') {
    return getBaseURL() + "api/v2/" + module;
  },
  toFormData(obj, form, namespace) {
    let fd = form || new FormData();
    let formKey;

    for (let property in obj) {
    //   if (obj.hasOwnProperty(property) && obj[property]) {
        if (obj.hasOwnProperty(property)) {
        if (namespace) {
          formKey = namespace + "[" + property + "]";
        } else {
          formKey = property;
        }

        // if the property is an object, but not a File, use recursivity.
        if (obj[property] instanceof Date) {
          fd.append(formKey, obj[property].toISOString());
        } else if (typeof obj[property] === "object" && !(obj[property] instanceof File)) {
          this.toFormData(obj[property], fd, formKey);
        } else {
          // if it's a string or a File object
          fd.append(formKey, obj[property]);
        }
      }
    }

    return fd;
  },
};
