const getRandomPosition = (maxDistance, getRandom) => ({
  x: getRandom() * maxDistance - maxDistance / 2,
  y: getRandom() * maxDistance - maxDistance / 2
});

const constructFirstCurve = nextPosition => {
  return new THREE.CubicBezierCurve(
    new THREE.Vector2(0, 0),
    new THREE.Vector2(0, 0),
    new THREE.Vector2(nextPosition.x * -1, nextPosition.y * -1),
    new THREE.Vector2(0, 0)
  );
};

const constructEvasionCurve = (currentPosition, nextPosition) => {
  return new THREE.CubicBezierCurve(
    new THREE.Vector2(0, 0),
    new THREE.Vector2(currentPosition.x, currentPosition.y),
    new THREE.Vector2(nextPosition.x * -1, nextPosition.y * -1),
    new THREE.Vector2(0, 0)
  );
};

const constructEvasionCurves = (evasion, maxDistance, getRandom) => {
  const curves = [];

  if (evasion === 0) {
    return [];
  }

  const positions = new Array(evasion + 1)
    .fill(0)
    .map(i => getRandomPosition(maxDistance, getRandom));

  for (let i = 0; i < positions.length; i++) {
    const position = positions[i];
    const nextPosition =
      i === positions.length - 1 ? { x: 0, y: 0 } : positions[i + 1];

    if (i === 0) {
      curves.push(constructFirstCurve(nextPosition));
    } else {
      curves.push(constructEvasionCurve(position, nextPosition));
    }
  }

  return curves;
};

class ShipEvasionMovementPath {
  constructor(seed, evasion) {
    this.evasion = evasion;

    this.curves = constructEvasionCurves(
      this.evasion,
      100 / this.evasion,
      mathlib.getSeededRandomGenerator(seed)
    );
  }

  getOffset(percent) {
    if (this.evasion === 0) {
      return { x: 0, y: 0 };
    }

    const point = (this.curves.length - 1) * percent;
    const curveNumber = Math.floor(point);
    const decimal = point - Math.floor(point);

    const curve = this.curves[curveNumber];
    return curve.getPoint(decimal);
  }
}

export default ShipEvasionMovementPath;
