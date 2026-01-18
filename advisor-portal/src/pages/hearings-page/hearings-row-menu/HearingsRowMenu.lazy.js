import React, { lazy, Suspense } from 'react';

const LazyHearingsRowMenu = lazy(() => import('./HearingsRowMenu'));

const HearingsRowMenu = props => (
  <Suspense fallback={null}>
    <LazyHearingsRowMenu {...props} />
  </Suspense>
);

export default HearingsRowMenu;
