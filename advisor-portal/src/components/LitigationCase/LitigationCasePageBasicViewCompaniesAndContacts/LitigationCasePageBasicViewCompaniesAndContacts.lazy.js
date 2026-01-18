import React, { lazy, Suspense } from 'react';

const LazyLitigationCasePageBasicViewCompaniesAndContacts = lazy(() => import('./LitigationCasePageBasicViewCompaniesAndContacts'));

const LitigationCasePageBasicViewCompaniesAndContacts = props => (
  <Suspense fallback={null}>
    <LazyLitigationCasePageBasicViewCompaniesAndContacts {...props} />
  </Suspense>
);

export default LitigationCasePageBasicViewCompaniesAndContacts;
