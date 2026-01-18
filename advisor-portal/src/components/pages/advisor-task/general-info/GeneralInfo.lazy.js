import React, { lazy, Suspense } from 'react';

const LazyGeneralInfo = lazy(() => import('./GeneralInfo'));

const GeneralInfo = props => (
  <Suspense fallback={null}>
    <LazyGeneralInfo {...props} />
  </Suspense>
);

export default GeneralInfo;
