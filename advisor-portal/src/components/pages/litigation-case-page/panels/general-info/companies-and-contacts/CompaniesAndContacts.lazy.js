import React, { lazy, Suspense } from 'react';

const LazyCompaniesAndContacts = lazy(() => import('./CompaniesAndContacts'));

const CompaniesAndContacts = props => (
  <Suspense fallback={null}>
    <LazyCompaniesAndContacts {...props} />
  </Suspense>
);

export default CompaniesAndContacts;
