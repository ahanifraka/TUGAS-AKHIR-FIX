## Docker Build & Turnstile Configuration

- Frontend site key: pass `VITE_TURNSTILE_SITE_KEY` during image build so Vite embeds it in the compiled assets.
  - Example: `docker build -t registry.gitlab.com/bpbumd/website:1.1.0-alpha -f docker/Dockerfile --build-arg VITE_TURNSTILE_SITE_KEY=1x00000000000000000000AA .`
- Backend secret key: supply `TURNSTILE_SECRET_KEY` at container runtime (do not bake secrets into images).
  - Example: `docker run -e TURNSTILE_SECRET_KEY=1x0000000000000000000000000000000AA -p 9000:9000 registry.gitlab.com/bpbumd/website:1.1.0-alpha`
- Alternatively, set these in your Compose file under `environment:` for the respective services.

```
sudo docker build  --no-cache \
--build-arg HTTP_PROXY=http://10.15.3.21:80 \
--build-arg HTTPS_PROXY=http://10.15.3.21:80 \
--build-arg http_proxy=http://10.15.3.21:80 \
--build-arg https_proxy=http://10.15.3.21:80 \
-t registry.gitlab.com/bpbumd/website -f docker/Dockerfile .
```