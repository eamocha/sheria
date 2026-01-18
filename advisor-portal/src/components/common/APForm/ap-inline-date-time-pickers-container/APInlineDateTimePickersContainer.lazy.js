import React, { lazy, Suspense } from 'react';

const LazyAPInlineDateTimePickersContainer = lazy(() => import('./APInlineDateTimePickersContainer'));

const APInlineDateTimePickersContainer = props => (
  <Suspense fallback={null}>
    <LazyAPInlineDateTimePickersContainer {...props} />
  </Suspense>
);

export default APInlineDateTimePickersContainer;
