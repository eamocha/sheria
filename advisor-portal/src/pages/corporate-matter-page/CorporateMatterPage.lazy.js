import React, { lazy, Suspense } from 'react';

const LazyCorporateMatterPage = lazy(() => import('./CorporateMatterPage'));

const CorporateMatterPage = props => (
  <Suspense fallback={null}>
    <LazyCorporateMatterPage {...props} />
  </Suspense>
);

export default CorporateMatterPage;
