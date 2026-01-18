import React, { lazy, Suspense } from 'react';

const LazyGeneralInfoItem = lazy(() => import('./GeneralInfoItem'));

const GeneralInfoItem = props => (
  <Suspense fallback={null}>
    <LazyGeneralInfoItem {...props} />
  </Suspense>
);

export default GeneralInfoItem;
