import React, { lazy, Suspense } from 'react';

const LazyCorporateMattersPage = lazy(() => import('./CorporateMattersPage'));

const CorporateMattersPage = props => (
  <Suspense fallback={null}>
    <LazyCorporateMattersPage {...props} />
  </Suspense>
);

export default CorporateMattersPage;
